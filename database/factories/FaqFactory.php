<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => 'سؤال تجريبي؟',
            'answer' => 'إجابة تجريبية مختصرة.',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
