import dotenv from 'dotenv';
dotenv.config();

export const SESSION_NAME = process.env.SESSION_NAME || 'biblioteca-bot';
export const CRON_SCHEDULE = process.env.CRON_SCHEDULE || '0 9 * * *';
export const CRON_REMINDER_SCHEDULE = process.env.CRON_REMINDER_SCHEDULE || '0 10 * * *';