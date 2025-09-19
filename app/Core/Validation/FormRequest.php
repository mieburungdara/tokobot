<?php

namespace TokoBot\Core\Validation;

use TokoBot\Helpers\Session;

/**
 * Base class for form request validation.
 */
abstract class FormRequest
{
    protected array $data;
    protected array $errors = [];

    public function __construct()
    {
        // For simplicity, we'll only handle POST data for now.
        $this->data = $_POST;
    }

    /**
     * Defines the validation rules.
     * Example: ['email' => 'required|email', 'password' => 'required|min:8']
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Validates the request data against the defined rules.
     *
     * @return bool True if validation passes, false otherwise.
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the original input data.
     *
     * @return array
     */
    public function getOldInput(): array
    {
        return $this->data;
    }

    /**
     * Applies a single validation rule to a field.
     *
     * @param string $field
     * @param mixed $value
     * @param string $rule
     */
    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $param = null;
        if (str_contains($rule, ':')) {
            [$rule, $param] = explode(':', $rule, 2);
        }

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = "The {$field} field is required.";
                }
                break;
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "The {$field} must be a valid email address.";
                }
                break;
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "The {$field} must be a number.";
                }
                break;
            case 'min':
                if (!empty($value) && strlen(trim($value)) < $param) {
                    $this->errors[$field][] = "The {$field} must be at least {$param} characters.";
                }
                break;
            case 'max':
                if (!empty($value) && strlen(trim($value)) > $param) {
                    $this->errors[$field][] = "The {$field} may not be greater than {$param} characters.";
                }
                break;
        }
    }

    /**
     * Redirects back to the previous page with errors and old input.
     */
    public function redirectBack(): void
    {
        Session::flash('validation_errors', $this->getErrors());
        Session::flash('old_input', $this->getOldInput());

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit();
    }
}
