<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Log;

use App\Services\FormAService;

class FastVenueRule implements Rule
{
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $fast_course_id = request('fast_course_id');

        $formAService = new FormAService();
        $fastCourse = $formAService->getFastCourse($fast_course_id);

        if (is_object($fastCourse)) {
            $days = $fastCourse->days ?? 1;
            $days = (integer) $days;
        } else {
            $days = 1;
        }

        if (count($value) === $days) {
            return true;
        } else {
            $this->message = ($days === 1) ? '一つご選択ください。' : $days . '日間ご選択ください。';
            return false;
        }



        if ($fast_course_id == 3) {
            if (count($value) == 2) {
                return true;
            } else {
                $this->message = '2日間ご選択ください。';
                return false;
            }
        } else {
            if (count($value) == 1) {
                return true;
            } else {
                $this->message = '一つご選択ください。';
                return false;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
