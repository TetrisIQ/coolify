<div>
    <form wire:submit.prevent='submit' class="flex flex-col">
        <div class="flex items-center gap-2">
            <h2>Telegram</h2>
            <x-forms.button type="submit">
                Save
            </x-forms.button>
            @if ($team->telegram_enabled)
                <x-forms.button class="text-white normal-case btn btn-xs no-animation btn-primary"
                    wire:click="sendTestNotification">
                    Send Test Notifications
                </x-forms.button>
            @endif
        </div>
        <div class="w-48">
            <x-forms.checkbox instantSave id="team.telegram_enabled" label="Notification Enabled" />
        </div>
        <div class="flex gap-2">
            <x-forms.input type="password"
                helper="Get it from the <a class='inline-block text-white underline' href='https://t.me/botfather' target='_blank'>BotFather Bot</a> on Telegram."
                required id="team.telegram_token" label="Token" />
            <x-forms.input
                helper="Recommended to add your bot to a group chat and add its Chat ID here." required
                id="team.telegram_chat_id" label="Chat ID" />
        </div>
    @if (data_get($team, 'telegram_enabled'))
        <h2 class="mt-4">Subscribe to events</h2>
        <div class="w-96">
            @if (isDev())
            <h3 class="mt-4">Test</h3>
                <div class="flex items-end gap-10">
                    <x-forms.checkbox instantSave="saveModel" id="team.telegram_notifications_test" label="Enabled" />
                    <x-forms.input
                        helper="If you are using Group chat with Topics, you can specify the topics ID. If empty, General topic will be used."
                        id="team.telegram_notifications_test_message_thread_id" label="Custom Topic ID" />
                </div>
            @endif
            <h3 class="mt-4">Container Status Changes</h3>
            <div class="flex items-end gap-10">
                <x-forms.checkbox instantSave="saveModel" id="team.telegram_notifications_status_changes"
                label="Enabled" />
                <x-forms.input
                    helper="If you are using Group chat with Topics, you can specify the topics ID. If empty, General topic will be used."
                    id="team.telegram_notifications_status_changes_message_thread_id" label="Custom Topic ID" />
            </div>
            <h3 class="mt-4">Application Deployments</h3>
            <div class="flex items-end gap-10">
                <x-forms.checkbox instantSave="saveModel" id="team.telegram_notifications_deployments"
                label="Enabled" />
                <x-forms.input
                    helper="If you are using Group chat with Topics, you can specify the topics ID. If empty, General topic will be used."
                    id="team.telegram_notifications_deployments_message_thread_id" label="Custom Topic ID" />
            </div>
            <h3 class="mt-4">Backup Status</h3>
            <div class="flex items-end gap-10">
                <x-forms.checkbox instantSave="saveModel" id="team.telegram_notifications_database_backups"
                label="Enabled" />
                <x-forms.input
                    helper="If you are using Group chat with Topics, you can specify the topics ID. If empty, General topic will be used."
                    id="team.telegram_notifications_database_backups_message_thread_id" label="Custom Topic ID" />
            </div>
        </div>
        @endif
    </form>
</div>
