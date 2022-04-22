<?php
/**
 * @var \marketingsolutions\scheduling\Schedule $schedule
 */

/**
 * Send emails from admin panel
 */
$schedule->command('mailing/mails/send');
/**
 * Removing orphaned documents
 */
$schedule->command('sales/sale-documents/remove-orphaned')->hourly();

/** Certificates */
$schedule->command('catalog/zakazpodarka-orders/create-soap --method=processed')->cron('30 * * * * *');
$schedule->command('catalog/zakazpodarka-orders/check-status --interval=600');
$schedule->command('catalog/card-items/send-to-user')->everyTenMinutes();

/** Payments */
$schedule->command('payments/process/index')->everyMinute();
$schedule->command('payments/process/check')->everyNMinutes(3);

/** New module Check-balance command */
$schedule->command('checker/check/check-card')->cron('00 08 * * *');

/** SMS service */
$schedule->command('sms/send/run')->everyMinute();

/** Updates paid_at from 1C */
$schedule->command('payments/process/update')->cron('30 05 * * *');