<?php

namespace App\Enums;

enum VotingMode: string
{
    case Participant = 'participant';
    case ParticipantAndAward = 'participant_and_award';

    public function label(): string
    {
        return match ($this) {
            self::Participant => 'One Vote per Mobile Number (Overall Participant)',
            self::ParticipantAndAward => 'One Vote per Award Category (Participant + Award)',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Participant => 'A mobile number can only vote for one participant across the entire event.',
            self::ParticipantAndAward => 'A mobile number can vote for a chosen participant under each active award category configured on the event.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
