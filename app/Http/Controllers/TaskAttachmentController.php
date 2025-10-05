<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskAttachmentController extends Controller
{
  public function store(Request $request, Project $project, Task $task)
  {
    // Verify task belongs to project
    if ($task->project_id !== $project->id) {
      abort(404, 'Task not found in this project');
    }

    $request->validate([
      'files.*' => 'required|file|max:10240', // 10MB max per file
      'descriptions.*' => 'nullable|string|max:255',
    ]);

    $attachments = [];

    if ($request->hasFile('files')) {
      foreach ($request->file('files') as $index => $file) {
        $originalName = $file->getClientOriginalName();
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('task-attachments', $filename, 'public');

        $attachment = TaskAttachment::create([
          'task_id' => $task->id,
          'user_id' => auth()->id(),
          'original_name' => $originalName,
          'filename' => $filename,
          'path' => $path,
          'mime_type' => $file->getMimeType(),
          'size' => $file->getSize(),
          'description' => $request->descriptions[$index] ?? null,
        ]);

        $attachments[] = $attachment->load('user');
      }
    }

    return back()->with('success', 'Files uploaded successfully');
  }

  public function download(TaskAttachment $attachment)
  {
    if (!Storage::disk('public')->exists($attachment->path)) {
      abort(404, 'File not found');
    }

    return response()->download(Storage::disk('public')->path($attachment->path), $attachment->original_name);
  }

  public function destroy(TaskAttachment $attachment)
  {
    // Check if user can delete this attachment (owner or admin)
    if (auth()->id() !== $attachment->user_id && !auth()->user()->hasRole('admin')) {
      abort(403, 'Unauthorized to delete this attachment');
    }

    // Delete file from storage
    if (Storage::disk('public')->exists($attachment->path)) {
      Storage::disk('public')->delete($attachment->path);
    }

    $attachment->delete();

    session()->flash('flash.banner', trans('words.attachment_deleted_successfully'));
    session()->flash('flash.bannerStyle', 'danger');
    // return response()->json([
    //   'message' => '',
    // ]);
  }

  public function update(Request $request, TaskAttachment $attachment)
  {
    $request->validate([
      'description' => 'nullable|string|max:255',
    ]);

    // Check if user can update this attachment (owner or admin)
    if (auth()->id() !== $attachment->user_id && !auth()->user()->hasRole('admin')) {
      abort(403, 'Unauthorized to update this attachment');
    }

    $attachment->update($request->only('description'));

    return response()->json([
      'message' => 'Attachment description updated successfully',
      'attachment' => $attachment->load('user'),
    ]);
  }
}
