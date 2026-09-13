<?php

namespace App\Policies;

use App\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permits('testimonials.manage');
    }

    public function view(User $user, Testimonial $testimonial): bool
    {
        return $user->permits('testimonials.manage');
    }

    public function create(User $user): bool
    {
        return $user->permits('testimonials.manage');
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->permits('testimonials.manage');
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->permits('testimonials.manage');
    }

    public function restore(User $user, Testimonial $testimonial): bool
    {
        return $user->permits('testimonials.manage');
    }

    public function forceDelete(User $user, Testimonial $testimonial): bool
    {
        return $user->permits('testimonials.manage');
    }
}
