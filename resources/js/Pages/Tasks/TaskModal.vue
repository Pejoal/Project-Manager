<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, ref, watch } from 'vue';

const emit = defineEmits(['close', 'refresh']);
const props = defineProps({
  show: Boolean,
  task: {
    type: Object,
    default: null,
  },
});

// File upload state
const fileInput = ref(null);
const uploadingFiles = ref(false);
const selectedFiles = ref([]);
const fileDescriptions = ref([]);

// Attachment management
const editingAttachment = ref(null);
const attachmentDescriptionForm = useForm({
  description: '',
});

// File upload form
const uploadForm = useForm({
  files: [],
  descriptions: [],
});

// Computed properties
const hasAttachments = computed(() => props.task?.attachments?.length > 0);

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  return new Date(datetime).toLocaleString();
};

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

// File upload methods
const selectFiles = () => {
  fileInput.value.click();
};

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

  // Reset the upload form
  uploadForm.clearErrors();

  // Prepare form data
  uploadForm.files = selectedFiles.value;
  uploadForm.descriptions = fileDescriptions.value;

  uploadForm.post(
    route('task-attachments.store', {
      project: props.task.project.slug,
      task: props.task.id,
    }),
    {
      forceFormData: true, // This ensures files are sent as FormData
      preserveScroll: true,
      onSuccess: () => {
        // Reset form
        selectedFiles.value = [];
        fileDescriptions.value = [];
        if (fileInput.value) {
          fileInput.value.value = '';
        }
        uploadForm.reset();

        // Refresh the task data
        emit('refresh');

        // Show success message
        alert(trans('words.file_uploaded_successfully'));
      },
      onError: (errors) => {
        console.error('Upload errors:', errors);

        // Handle validation errors
        let errorMessage = trans('words.error_uploading_files');

        if (errors.files) {
          errorMessage = Array.isArray(errors.files) ? errors.files[0] : errors.files;
        } else if (errors.message) {
          errorMessage = errors.message;
        } else if (Object.keys(errors).length > 0) {
          // Get first error message
          const firstError = Object.values(errors)[0];
          errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        }

        alert(errorMessage);
      },
      onFinish: () => {
        uploadingFiles.value = false;
      },
    }
  );
};

// Attachment management methods
const downloadAttachment = (attachment) => {
  window.open(route('task-attachments.download', attachment.id), '_blank');
};

const deleteAttachment = async (attachment) => {
  if (!confirm(`Are you sure you want to delete "${attachment.original_name}"?`)) {
    return;
  }

  const deleteForm = useForm({});

  deleteForm.delete(route('task-attachments.destroy', attachment.id), {
    preserveScroll: true,
    onSuccess: () => {
      emit('refresh');
      alert(trans('words.attachment_deleted_successfully'));
    },
    onError: (errors) => {
      console.error('Delete error:', errors);
      alert('Error deleting attachment. Please try again.');
    },
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

const updateAttachmentDescription = async () => {
  try {
    await attachmentDescriptionForm.put(route('task-attachments.update', editingAttachment.value.id));
    editingAttachment.value = null;
    emit('refresh');
    alert('Description updated successfully!');
  } catch (error) {
    console.error('Update error:', error);
    alert('Error updating description. Please try again.');
  }
};

// Reset form when modal closes
watch(
  () => props.show,
  (isShowing) => {
    if (!isShowing) {
      selectedFiles.value = [];
      fileDescriptions.value = [];
      editingAttachment.value = null;
      attachmentDescriptionForm.reset();
      if (fileInput.value) {
        fileInput.value.value = '';
      }
    }
  }
);
</script>

<template>
  <DialogModal :show="show" @close="emit('close')" max-width="4xl">
    <template #title>
      <div class="flex items-center justify-between">
        <span>{{ task?.name || 'Task Details' }}</span>
        <div class="flex items-center space-x-2">
          <span
            v-if="task?.status"
            class="px-2 py-1 text-xs font-semibold rounded-full text-white"
            :style="{ backgroundColor: task.status.color }"
          >
            {{ task.status.name }}
          </span>
          <span
            v-if="task?.priority"
            class="px-2 py-1 text-xs font-semibold rounded-full text-white"
            :style="{ backgroundColor: task.priority.color }"
          >
            {{ task.priority.name }}
          </span>
        </div>
      </div>
    </template>

    <template #content>
      <div v-if="task" class="space-y-6">
        <!-- Basic Information Section -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ trans('words.basic_data') }}
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.task_id') }}
              </label>
              <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">#{{ task.id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.project') }}
              </label>
              <Link
                :href="route('projects.show', task.project.slug)"
                class="mt-1 text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ task.project.name }}
              </Link>
            </div>

            <div v-if="task.phase">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.phase') }}
              </label>
              <Link
                :href="route('phases.show', { project: task.project.slug, phase: task.phase.id })"
                class="mt-1 text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ task.phase.name }}
              </Link>
            </div>

            <div v-if="task.milestone">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.milestone') }}
              </label>
              <Link
                :href="route('milestones.show', { project: task.project.slug, milestone: task.milestone.id })"
                class="mt-1 text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ task.milestone.name }}
              </Link>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.description') }}
              </label>
              <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                {{ task.description || 'No description provided' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Assignment & Timing Section -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Assignment & Timing</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.assigned_to') }}
              </label>
              <div class="mt-1">
                <div v-if="task.assigned_to?.length > 0" class="flex flex-wrap gap-2">
                  <span
                    v-for="user in task.assigned_to"
                    :key="user.id"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100"
                  >
                    {{ user.name }}
                  </span>
                </div>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">Not assigned</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.created_at') }}
              </label>
              <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                {{ formatDateTime(task.created_at) }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.start_datetime') }}
              </label>
              <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                {{ formatDateTime(task.start_datetime) }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('words.end_datetime') }}
              </label>
              <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                {{ formatDateTime(task.end_datetime) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Attachments Section -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
              Attachments ({{ task.attachments?.length || 0 }})
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
                <div class="flex items-center space-x-3 flex-1">
                  <span class="text-2xl">{{ getFileIcon(file.type) }}</span>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                      {{ file.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatFileSize(file.size) }}
                    </p>
                  </div>
                  <div class="w-48">
                    <TextInput v-model="fileDescriptions[index]" placeholder="Optional description" class="text-xs" />
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
              >
                Cancel
              </SecondaryButton>
              <PrimaryButton @click="uploadFiles" :disabled="uploadingFiles">
                <span v-if="uploadingFiles">Uploading...</span>
                <span v-else>Upload Files</span>
              </PrimaryButton>
            </div>
          </div>

          <!-- Existing Attachments -->
          <div v-if="hasAttachments" class="space-y-3">
            <div
              v-for="attachment in task.attachments"
              :key="attachment.id"
              class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600"
            >
              <div class="flex items-center space-x-3 flex-1">
                <span class="text-2xl">{{ getFileIcon(attachment.mime_type) }}</span>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ attachment.original_name }}
                  </p>
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
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                          />
                        </svg>
                      </button>
                    </div>
                    <InputError class="mt-1" :message="attachmentDescriptionForm.errors.description" />
                  </div>
                  <div v-else-if="attachment.description" class="mt-1">
                    <p class="text-xs text-gray-600 dark:text-gray-400 italic">
                      {{ attachment.description }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex items-center space-x-2">
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

          <div v-else-if="!hasAttachments" class="text-center py-8">
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
        </div>
      </div>

      <!-- Hidden file input -->
      <input ref="fileInput" type="file" multiple class="hidden" @change="handleFileSelect" accept="*/*" />
    </template>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <div class="flex space-x-2">
          <Link
            v-if="task"
            :href="route('tasks.edit', { project: task.project.slug, task: task.id })"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
          >
            {{ trans('words.edit') }}
          </Link>
          <Link
            v-if="task"
            :href="route('time-entries.index', { task_id: task.id })"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
          >
            {{ trans('words.log_time') }}
          </Link>
        </div>

        <SecondaryButton @click="emit('close')">
          {{ trans('words.close') }}
        </SecondaryButton>
      </div>
    </template>
  </DialogModal>
</template>
