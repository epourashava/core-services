<?php

namespace Core\Services\SMS;

enum SmsEnum: string
{
    case ELITBUZZ = 'elitbuzz';
    case GREENHERITAGE = 'greenheritage';

    case MASKED = 'masked';
    case NON_MASKED = 'non-masked';
}
