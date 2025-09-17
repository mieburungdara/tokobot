<?php

namespace TokoBot\BotHandlers\Commands;

use TelegramBot\Types\Update;

/**
 * Interface CommandInterface
 *
 * Defines the contract for a bot command handler.
 */
interface CommandInterface
{
    /**
     * Handles the incoming command.
     *
     * @param Update $update The full update object from Telegram.
     * @param array $args Additional arguments that might be passed to the command.
     * @return void
     */
    public function handle(Update $update, array $args = []): void;
}
