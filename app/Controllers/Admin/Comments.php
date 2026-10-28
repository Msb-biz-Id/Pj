<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommentModel;

class Comments extends BaseController
{
    public function index()
    {
        $commentModel = new CommentModel();
        $comments = $commentModel->select('comments.*, posts.title as post_title, posts.slug as post_slug')
            ->join('posts', 'posts.id = comments.post_id', 'left')
            ->orderBy('comments.created_at', 'DESC')
            ->findAll();

        $data = [
            'metaTitle' => 'Moderasi Komentar Blog — Admin Pusat Jersey',
            'comments'  => $comments,
        ];

        return $this->renderAdminView('admin/comments/index', $data);
    }

    /**
     * Approve a comment (set status = 'approved')
     */
    public function approve($id)
    {
        $commentModel = new CommentModel();
        $commentModel->update($id, ['status' => 'approved']);

        return redirect()->to('/admin/comments');
    }

    /**
     * Mark as spam
     */
    public function spam($id)
    {
        $commentModel = new CommentModel();
        $commentModel->update($id, ['status' => 'spam']);

        return redirect()->to('/admin/comments');
    }

    /**
     * Delete permanently
     */
    public function delete($id)
    {
        $commentModel = new CommentModel();
        $commentModel->delete($id);

        return redirect()->to('/admin/comments');
    }
}
