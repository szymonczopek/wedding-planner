<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as FrameworkFormRequest;

abstract class FormRequest extends FrameworkFormRequest
{
    public function messages(): array
    {
        return [
            'required' => 'Wypełnij to pole',
            'string' => 'Wartość musi być tekstem',
            'email.unique' => 'Ten adres e-mail posiada już konto',
            'date' => 'Podaj datę',
            'password.confirmed' => 'Powtórz hasło',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nazwa',
            'email' => 'e-mail',
            'password' => 'hasło',
            'date' => 'data',
            'role' => 'rola',
        ];
    }
}
