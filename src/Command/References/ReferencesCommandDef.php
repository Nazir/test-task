<?php

namespace App\Command\References;

/**
 * Definitions for commands
 */
final class ReferencesCommandDef
{
    /**
     * Commands
     */
    /** @var string APP_COMMAND_FILL_DATA Fill data */
    public const APP_COMMAND_FILL_DATA = 'app:references:fill-data';
    /** @var string APP_COMMAND_FILL_DATA_FROM_CSV Fill data from CSV files */
    public const APP_COMMAND_FILL_DATA_FROM_CSV = 'app:references:fill-data-from-csv';
}
