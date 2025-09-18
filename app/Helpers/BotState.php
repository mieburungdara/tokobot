<?php

namespace TokoBot\Helpers;

class BotState
{
    // General states
    public const IDLE = 'idle';
    public const CANCELLED = 'cancelled';

    // Selling process states
    public const SELLING_BATCHING_ITEMS = 'selling_batching_items';
    public const SELLING_AWAITING_PRICE = 'selling_awaiting_price'; // This state is implicitly handled by GenericBotHandler
    public const SELLING_AWAITING_CONFIRMATION = 'selling_awaiting_confirmation';
    public const SELLING_FINALIZED = 'selling_finalized';
}
