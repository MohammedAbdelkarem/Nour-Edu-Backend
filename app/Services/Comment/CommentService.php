<?php

namespace App\Services\Comment;

use App\Models\Lesson;
use App\Models\Replay;
use App\Models\Comment;
use App\Enums\CommentStatusEnum;

/**
 * Class CommentService.
 */
class CommentService
{
    public function getForMobile($data , $lesson_id)
    {
        $comments =  Comment::exist()->where('lesson_id', $lesson_id)
                ->with('existReplay');

        return getOrPaginate($comments, $data);
    }

    public function store($data , $lesson_id)
    {
        if(! is_commented($lesson_id)) {
            Comment::create([
                'lesson_id' => $lesson_id,
                'user_id' => auth()->id(),
                'text' => $data['text'],
            ]);
        }
    }

    public function deleteComment($id)
    {
        
        Comment::findByIdOrFail($id)->update([
            'status' => $this->determineDeletedType(),
        ]);

        Replay::where('comment_id', $id)->update([
            'status' => $this->determineDeletedType(),
        ]);
    }

    public function replay($data , $comment_id)
    {
        if(! is_replayed($comment_id)) {
            Replay::create([
                'comment_id' => $comment_id,
                'user_id' => auth()->id(),
                'text' => $data['text'],
            ]);
        }
    }

    public function deleteReplay($id)
    {
        Replay::findByIdOrFail($id)->update([
            'status' => $this->determineDeletedType(),
        ]);
    }

    public function pinComment($comment_id)
    {
        $comment = Comment::findByIdOrFail($comment_id);
        $lesson = Lesson::findByIdOrFail($comment->lesson_id);

        if(! $comment->is_pinned) 
        {
            $lesson->comments()->update([
                'is_pinned' => false,
            ]);
            $comment->update([
                'is_pinned' => true,
            ]);
        }
        else
        {
            $comment->update([
                'is_pinned' => false,
            ]);
        }


        $lesson->save();
        $comment->save();
    }

    private function determineDeletedType()
    {
        if(auth()->user()->isTeacher())
            return CommentStatusEnum::DELETED_BY_TEACHER->value;
        if(auth()->user()->isAdmin())
            return CommentStatusEnum::DELETED_BY_ADMIN->value;
        return CommentStatusEnum::DELETED_BY_STUDENT->value;
    }
    
}
