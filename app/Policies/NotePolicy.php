<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the note.
     */
    public function view(User $user, Note $note): bool
    {
        return $note->user_id === $user->id || $note->is_public;
    }

    /**
     * Determine whether the user can create notes.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create notes
    }

    /**
     * Determine whether the user can update the note.
     */
    public function update(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the note.
     */
    public function delete(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the note.
     */
    public function restore(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the note.
     */
    public function forceDelete(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }
}