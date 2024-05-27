<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user;

    #[Validate('required')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    // Optional
    #[Validate('sometimes')]
    public ?int $country_id = null;

    #[Validate('nullable|image|max:1024')]
    public $photo;

    // Selected languages
    #[Validate('required')]
    public array $my_languages = [];

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->country_id = $user->country_id;

        // Fill the selected languages property
        $this->my_languages = $user->languages->pluck('id')->all();
    }

    public function update(): void
    {
        // Validate
        $data = $this->validate();

        // Update
        $this->user->update($data);

        // Upload file and save the avatar `url` on User model
        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        // Sync selection
        $this->user->languages()->sync($this->my_languages);
    }
}