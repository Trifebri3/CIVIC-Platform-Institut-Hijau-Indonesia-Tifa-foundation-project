<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ActivationQuestion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QuestionBuilder extends Component
{
    use WithFileUploads;

    // Properti Utama
    public $questionId, $title, $story, $image, $existingImage, $example_answer;
    public $order = 0, $is_active = true;

    // Properti JSON (Array of Inputs)
    public $response_definitions = [];

    public function mount($question = null)
    {
        if ($question) {
            $this->questionId = $question->id;
            $this->title = $question->title;
            $this->story = $question->story;
            $this->existingImage = $question->image;
            $this->example_answer = $question->example_answer;
            $this->order = $question->order;
            $this->is_active = $question->is_active;
            $this->response_definitions = $question->response_definitions ?? [];
        } else {
            $this->addField(); // Start dengan 1 field kosong
        }
    }

    // Fungsi Tambah Baris Input Baru
    public function addField()
    {
        $this->response_definitions[] = [
            'id' => 'field_' . Str::random(5),
            'type' => 'text', // text, textarea, select, checkbox, file
            'label' => '',
            'required' => false,
            'options' => '' // Untuk select/checkbox pisahkan dengan koma
        ];
    }

    // Fungsi Hapus Baris Input
    public function removeField($index)
    {
        unset($this->response_definitions[$index]);
        $this->response_definitions = array_values($this->response_definitions);
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:5',
            'response_definitions.*.label' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'story' => $this->story,
            'example_answer' => $this->example_answer,
            'response_definitions' => $this->response_definitions,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            // Hapus gambar lama jika ada
            if ($this->existingImage) Storage::disk('public')->delete($this->existingImage);
            $data['image'] = $this->image->store('questions', 'public');
        }

        ActivationQuestion::updateOrCreate(['id' => $this->questionId], $data);

        session()->flash('success', 'Konfigurasi Task Berhasil Disimpan!');
        return redirect()->route('superadmin.activation.index');
    }

    public function render()
    {
        return view('livewire.super-admin.question-builder');
    }
}
