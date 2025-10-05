<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class KnowledgeBaseArticleController extends Controller
{
  public function index(Request $request)
  {
    // Gate::authorize('viewAny', KnowledgeBaseArticle::class);

    $articles = KnowledgeBaseArticle::with(['author'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->category, function ($query, $category) {
        $query->inCategory($category);
      })
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->when($request->featured, function ($query) {
        $query->featured();
      })
      ->when($request->tag, function ($query, $tag) {
        $query->withTag($tag);
      })
      ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $categories = KnowledgeBaseArticle::distinct()->pluck('category')->filter();

    return Inertia::render('CRM/KnowledgeBase/Index', [
      'articles' => $articles,
      'categories' => $categories,
      'filters' => $request->only(['search', 'category', 'status', 'featured', 'tag']),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create()
  {
    // Gate::authorize('create', KnowledgeBaseArticle::class);

    return Inertia::render('CRM/KnowledgeBase/Create');
  }

  public function store(Request $request)
  {
    // Gate::authorize('create', KnowledgeBaseArticle::class);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'content' => 'required|string',
      'excerpt' => 'nullable|string|max:500',
      'category' => 'required|string|max:255',
      'tags' => 'nullable|array',
      'status' => 'nullable|in:draft,published,archived',
      'is_featured' => 'nullable|boolean',
      'meta_description' => 'nullable|string|max:160',
      'attachments' => 'nullable|array',
    ]);

    $validated['author_id'] = auth()->id();
    $validated['slug'] = str()->slug($validated['title']);

    $article = KnowledgeBaseArticle::create($validated);

    return redirect()->route('knowledge-base.index')->with('success', __('Article created successfully.'));
  }

  public function show(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('view', $knowledgeBaseArticle);

    $knowledgeBaseArticle->load(['author']);
    $knowledgeBaseArticle->incrementViews();

    $relatedArticles = KnowledgeBaseArticle::published()
      ->inCategory($knowledgeBaseArticle->category)
      ->where('id', '!=', $knowledgeBaseArticle->id)
      ->limit(5)
      ->get();

    return Inertia::render('CRM/KnowledgeBase/Show', [
      'article' => $knowledgeBaseArticle,
      'relatedArticles' => $relatedArticles,
    ]);
  }

  public function edit(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    return Inertia::render('CRM/KnowledgeBase/Edit', [
      'article' => $knowledgeBaseArticle,
    ]);
  }

  public function update(Request $request, KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'content' => 'required|string',
      'excerpt' => 'nullable|string|max:500',
      'category' => 'required|string|max:255',
      'tags' => 'nullable|array',
      'status' => 'nullable|in:draft,published,archived',
      'is_featured' => 'nullable|boolean',
      'meta_description' => 'nullable|string|max:160',
      'attachments' => 'nullable|array',
    ]);

    // Update slug if title changed
    if ($validated['title'] !== $knowledgeBaseArticle->title) {
      $validated['slug'] = str()->slug($validated['title']);
    }

    $knowledgeBaseArticle->update($validated);

    return redirect()
      ->route('knowledge-base.show', $knowledgeBaseArticle)
      ->with('success', __('Article updated successfully.'));
  }

  public function destroy(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('delete', $knowledgeBaseArticle);

    $knowledgeBaseArticle->delete();

    return redirect()->route('knowledge-base.index')->with('success', __('Article deleted successfully.'));
  }

  public function publish(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $knowledgeBaseArticle->publish();

    return response()->json([
      'message' => __('Article published successfully.'),
      'article' => $knowledgeBaseArticle->fresh(),
    ]);
  }

  public function unpublish(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $knowledgeBaseArticle->unpublish();

    return response()->json([
      'message' => __('Article unpublished successfully.'),
      'article' => $knowledgeBaseArticle->fresh(),
    ]);
  }

  public function archive(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $knowledgeBaseArticle->archive();

    return response()->json([
      'message' => __('Article archived successfully.'),
      'article' => $knowledgeBaseArticle->fresh(),
    ]);
  }

  public function feature(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $knowledgeBaseArticle->feature();

    return response()->json([
      'message' => __('Article featured successfully.'),
      'article' => $knowledgeBaseArticle->fresh(),
    ]);
  }

  public function unfeature(KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $knowledgeBaseArticle->unfeature();

    return response()->json([
      'message' => __('Article unfeatured successfully.'),
      'article' => $knowledgeBaseArticle->fresh(),
    ]);
  }

  public function addTag(Request $request, KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $validated = $request->validate([
      'tag' => 'required|string|max:255',
    ]);

    $knowledgeBaseArticle->addTag($validated['tag']);

    return response()->json([
      'message' => __('Tag added successfully.'),
      'tags' => $knowledgeBaseArticle->fresh()->tags,
    ]);
  }

  public function removeTag(Request $request, KnowledgeBaseArticle $knowledgeBaseArticle)
  {
    // Gate::authorize('update', $knowledgeBaseArticle);

    $validated = $request->validate([
      'tag' => 'required|string',
    ]);

    $knowledgeBaseArticle->removeTag($validated['tag']);

    return response()->json([
      'message' => __('Tag removed successfully.'),
      'tags' => $knowledgeBaseArticle->fresh()->tags,
    ]);
  }

  public function categories()
  {
    // Gate::authorize('viewAny', KnowledgeBaseArticle::class);

    $categories = KnowledgeBaseArticle::published()
      ->selectRaw('category, COUNT(*) as article_count')
      ->groupBy('category')
      ->orderBy('category')
      ->get();

    return Inertia::render('CRM/KnowledgeBase/Categories', [
      'categories' => $categories,
    ]);
  }

  public function popular()
  {
    // Gate::authorize('viewAny', KnowledgeBaseArticle::class);

    $articles = KnowledgeBaseArticle::with(['author'])
      ->published()
      ->orderBy('view_count', 'desc')
      ->limit(20)
      ->get();

    return Inertia::render('CRM/KnowledgeBase/Popular', [
      'articles' => $articles,
    ]);
  }

  public function search(Request $request)
  {
    // Gate::authorize('viewAny', KnowledgeBaseArticle::class);

    $validated = $request->validate([
      'q' => 'required|string|min:3',
    ]);

    $articles = KnowledgeBaseArticle::with(['author'])
      ->published()
      ->search($validated['q'])
      ->paginate(15)
      ->withQueryString();

    return Inertia::render('CRM/KnowledgeBase/Search', [
      'articles' => $articles,
      'query' => $validated['q'],
    ]);
  }

  public function analytics(Request $request)
  {
    // Gate::authorize('viewAny', KnowledgeBaseArticle::class);

    $stats = [
      'total_articles' => KnowledgeBaseArticle::count(),
      'published_articles' => KnowledgeBaseArticle::published()->count(),
      'draft_articles' => KnowledgeBaseArticle::draft()->count(),
      'total_views' => KnowledgeBaseArticle::sum('view_count'),
      'avg_views_per_article' => KnowledgeBaseArticle::avg('view_count'),
      'featured_articles' => KnowledgeBaseArticle::featured()->count(),
    ];

    $topArticles = KnowledgeBaseArticle::with(['author'])
      ->orderBy('view_count', 'desc')
      ->limit(10)
      ->get();

    $categoriesStats = KnowledgeBaseArticle::selectRaw('category, COUNT(*) as count, SUM(view_count) as total_views')
      ->groupBy('category')
      ->orderBy('total_views', 'desc')
      ->get();

    $monthlyViews = KnowledgeBaseArticle::selectRaw(
      'DATE_FORMAT(updated_at, "%Y-%m") as month, SUM(view_count) as views'
    )
      ->whereBetween('updated_at', [now()->subMonths(11), now()])
      ->groupBy('month')
      ->orderBy('month')
      ->get();

    return Inertia::render('CRM/KnowledgeBase/Analytics', [
      'stats' => $stats,
      'topArticles' => $topArticles,
      'categoriesStats' => $categoriesStats,
      'monthlyViews' => $monthlyViews,
    ]);
  }

  public function bulkAction(Request $request)
  {
    // Gate::authorize('viewAny', KnowledgeBaseArticle::class);

    $validated = $request->validate([
      'action' => 'required|in:publish,unpublish,archive,feature,unfeature,delete',
      'article_ids' => 'required|array|min:1',
      'article_ids.*' => 'exists:knowledge_base_articles,id',
    ]);

    $articles = KnowledgeBaseArticle::whereIn('id', $validated['article_ids'])->get();

    foreach ($articles as $article) {
      // Gate::authorize('update', $article);
    }

    switch ($validated['action']) {
      case 'publish':
        foreach ($articles as $article) {
          $article->publish();
        }
        $message = __('Articles published successfully.');
        break;

      case 'unpublish':
        foreach ($articles as $article) {
          $article->unpublish();
        }
        $message = __('Articles unpublished successfully.');
        break;

      case 'archive':
        foreach ($articles as $article) {
          $article->archive();
        }
        $message = __('Articles archived successfully.');
        break;

      case 'feature':
        foreach ($articles as $article) {
          $article->feature();
        }
        $message = __('Articles featured successfully.');
        break;

      case 'unfeature':
        foreach ($articles as $article) {
          $article->unfeature();
        }
        $message = __('Articles unfeatured successfully.');
        break;

      case 'delete':
        KnowledgeBaseArticle::whereIn('id', $validated['article_ids'])->delete();
        $message = __('Articles deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }
}
