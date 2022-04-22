<?php
/**
 * @var \marketingsolutions\scheduling\Schedule $schedule
 */

/**
 * Removing orphaned documents
 */
$schedule->command('sales/sale-documents/remove-orphaned')->hourly();