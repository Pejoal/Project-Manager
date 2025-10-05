<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, ref, watch } from 'vue';

const emit = defineEmits(['refresh']);
const props = defineProps({
  attachments: {
    type: Array,
    default: () => [],
  },
  // The parameters needed to build the routes, e.g., { project: 'slug', task: 1 }
  routeParams: {
    type: Object,
    required: true,
  },
  // Route names for CRUD operations
  uploadRouteName: {
    type: String,
    required: true,
  },
  updateRouteName: {
    type: String,
    required: true,
  },
  deleteRouteName: {
    type: String,
    required: true,
  },
  downloadRouteName: {
    type: String,
    required: true,
  },
});

// --- State Management ---
const fileInput = ref(null);
const uploadingFiles = ref(false);
const selectedFiles = ref([]);
const fileDescriptions = ref([]);
const editingAttachment = ref(null);

// --- Forms ---
const uploadForm = useForm({
  files: [],
  descriptions: [],
});

const attachmentDescriptionForm = useForm({
  description: '',
});

// --- Computed Properties ---
const hasAttachments = computed(() => props.attachments?.length > 0);

// --- Helper Functions ---
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getFileIcon = (mimeType) => {
  if (mimeType.startsWith('image/')) return '🖼️';
  if (mimeType.includes('pdf')) return '📄';
  if (mimeType.includes('word')) return '📝';
  if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return '📊';
  if (mimeType.includes('zip') || mimeType.includes('rar')) return '🗜️';
  return '📁';
};

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  return new Date(datetime).toLocaleString();
};

// --- Methods ---
const selectFiles = () => fileInput.value.click();

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files);
  selectedFiles.value = files;
  fileDescriptions.value = new Array(files.length).fill('');
};

const removeSelectedFile = (index) => {
  selectedFiles.value.splice(index, 1);
  fileDescriptions.value.splice(index, 1);
};

const uploadFiles = () => {
  if (selectedFiles.value.length === 0) return;
  uploadingFiles.value = true;
  uploadForm.clearErrors();
  uploadForm.files = selectedFiles.value;
  uploadForm.descriptions = fileDescriptions.value;

  uploadForm.post(route(props.uploadRouteName, props.routeParams), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      selectedFiles.value = [];
      fileDescriptions.value = [];
      if (fileInput.value) fileInput.value.value = '';
      uploadForm.reset();
      emit('refresh');
      alert(trans('words.file_uploaded_successfully'));
    },
    onError: (errors) => {
      console.error('Upload errors:', errors);
      const firstError = Object.values(errors)[0];
      alert(Array.isArray(firstError) ? firstError[0] : firstError);
    },
    onFinish: () => {
      uploadingFiles.value = false;
    },
  });
};

const downloadAttachment = (attachment) => {
  window.open(route(props.downloadRouteName, attachment.id), '_blank');
};

const deleteAttachment = (attachment) => {
  if (!confirm(`Are you sure you want to delete "${attachment.original_name}"?`)) return;

  useForm({}).delete(route(props.deleteRouteName, attachment.id), {
    preserveScroll: true,
    onSuccess: () => {
      emit('refresh');
      alert(trans('words.attachment_deleted_successfully'));
    },
    onError: () => alert('Error deleting attachment.'),
  });
};

const startEditDescription = (attachment) => {
  editingAttachment.value = attachment;
  attachmentDescriptionForm.description = attachment.description || '';
};

const cancelEditDescription = () => {
  editingAttachment.value = null;
  attachmentDescriptionForm.reset();
};

const updateAttachmentDescription = () => {
  attachmentDescriptionForm.put(route(props.updateRouteName, editingAttachment.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      editingAttachment.value = null;
      emit('refresh');
      alert('Description updated successfully!');
    },
    onError: () => alert('Error updating description.'),
  });
};

// Reset local state on external changes
watch(
  () => props.attachments,
  () => {
    selectedFiles.value = [];
    fileDescriptions.value = [];
    editingAttachment.value = null;
    attachmentDescriptionForm.reset();
    if (fileInput.value) fileInput.value.value = '';
  }
);
</script>

<template>
  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        Attachments ({{ attachments?.length || 0 }})
      </h3>
      <SecondaryButton @click="selectFiles" :disabled="uploadingFiles">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Add Files
      </SecondaryButton>
    </div>

    <!-- File Upload Area -->
    <div
      v-if="selectedFiles.length > 0"
      class="mb-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
    >
      <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Files</h4>
      <div class="space-y-3">
        <div
          v-for="(file, index) in selectedFiles"
          :key="index"
          class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border"
        >
          <div class="flex items-center space-x-3 flex-1 min-w-0">
            <span class="text-2xl">{{ getFileIcon(file.type) }}</span>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ file.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(file.size) }}</p>
            </div>
            <div class="w-full md:w-48">
              <TextInput v-model="fileDescriptions[index]" placeholder="Optional description" class="text-xs w-full" />
            </div>
          </div>
          <button
            @click="removeSelectedFile(index)"
            class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="mt-3 flex justify-end space-x-2">
        <SecondaryButton
          @click="
            selectedFiles = [];
            fileDescriptions = [];
          "
          >Cancel</SecondaryButton
        >
        <PrimaryButton @click="uploadFiles" :disabled="uploadingFiles">
          {{ uploadingFiles ? 'Uploading...' : 'Upload Files' }}
        </PrimaryButton>
      </div>
    </div>

    <!-- Existing Attachments -->
    <div v-if="hasAttachments" class="space-y-3">
      <div
        v-for="attachment in attachments"
        :key="attachment.id"
        class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600"
      >
        <div class="flex items-center space-x-3 flex-1 min-w-0">
          <span class="text-2xl">{{ getFileIcon(attachment.mime_type) }}</span>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ attachment.original_name }}</p>
            <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
              <span>{{ attachment.formatted_size }}</span>
              <span>{{ attachment.user.name }}</span>
              <span>{{ formatDateTime(attachment.created_at) }}</span>
            </div>

            <!-- Description -->
            <div v-if="editingAttachment?.id === attachment.id" class="mt-2">
              <div class="flex items-center space-x-2">
                <TextInput
                  v-model="attachmentDescriptionForm.description"
                  placeholder="Add description"
                  class="text-xs flex-1"
                />
                <button
                  @click="updateAttachmentDescription"
                  :disabled="attachmentDescriptionForm.processing"
                  class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                <button
                  @click="cancelEditDescription"
                  class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <InputError class="mt-1" :message="attachmentDescriptionForm.errors.description" />
            </div>
            <div v-else-if="attachment.description" class="mt-1">
              <p class="text-xs text-gray-600 dark:text-gray-400 italic">{{ attachment.description }}</p>
            </div>
          </div>
        </div>
        <div class="flex items-center space-x-2 flex-shrink-0">
          <button
            @click="downloadAttachment(attachment)"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
            title="Download"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </button>
          <button
            @click="startEditDescription(attachment)"
            class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
            title="Edit description"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />
            </svg>
          </button>
          <button
            @click="deleteAttachment(attachment)"
            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
            title="Delete"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!hasAttachments && selectedFiles.length === 0" class="text-center py-8">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
        />
      </svg>
      <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No attachments yet</p>
      <p class="text-xs text-gray-400 dark:text-gray-500">Click "Add Files" to upload documents</p>
    </div>

    <!-- Hidden file input -->
    <input ref="fileInput" type="file" multiple class="hidden" @change="handleFileSelect" accept="*/*" />
  </div>
</template>
