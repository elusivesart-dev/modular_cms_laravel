<?php

declare(strict_types=1);

namespace Modules\Media\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Media\Application\Http\Requests\DeleteMediaRequest;
use Modules\Media\Application\Http\Requests\UploadMediaRequest;
use Modules\Media\Infrastructure\Models\Media;

final class MediaController extends Controller
{
    public function __construct(
        private readonly MediaServiceInterface $media,
    ) {
        $this->authorizeResource(Media::class, 'media');
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'mime_type' => $request->string('mime_type')->toString(),
        ];

        return view('media::media.index', [
            'mediaItems' => $this->media->paginate(
                $filters,
                (int) config('media.per_page', 24),
            ),
            'filters' => $filters,
            'pickerMode' => $request->boolean('picker'),
            'pickerTarget' => $request->string('target')->toString(),
        ]);
    }

    public function store(UploadMediaRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $this->media->upload(
            file: $request->file('file'),
            uploadedBy: $request->user() !== null ? (int) $request->user()->getAuthIdentifier() : null,
            title: $payload['title'],
            altText: $payload['alt_text'],
        );

        if ($request->boolean('picker')) {
            return redirect()
                ->route('media.index', [
                    'picker' => 1,
                    'target' => $request->string('target')->toString(),
                ])
                ->with('success', __('media::media.messages.uploaded'));
        }

        return redirect()
            ->route('media.index')
            ->with('success', __('media::media.messages.uploaded'));
    }

    public function destroy(DeleteMediaRequest $request, Media $media): RedirectResponse
    {
        $this->media->delete($media);

        if ($request->boolean('picker')) {
            return redirect()
                ->route('media.index', [
                    'picker' => 1,
                    'target' => $request->string('target')->toString(),
                ])
                ->with('success', __('media::media.messages.deleted'));
        }

        return redirect()
            ->route('media.index')
            ->with('success', __('media::media.messages.deleted'));
    }
}