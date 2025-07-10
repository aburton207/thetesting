<?php if ($notes) { ?>
    <div class="list-group">
        <?php foreach ($notes as $note) { ?>
            <div class="list-group-item">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm">
                            <img src="<?php echo get_avatar($note->created_by_avatar); ?>" alt="...">
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <strong><?php echo $note->created_by_user_name; ?></strong>
                        <small class="text-muted float-end"><?php echo format_to_datetime($note->created_at); ?></small>
                        <p><?php echo nl2br($note->description); ?></p>
                        <?php if ($note->title) { ?>
                            <small class="text-muted"><?php echo app_lang("title") . ": " . $note->title; ?></small>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <p><?php echo app_lang("no_notes_found"); ?></p>
<?php } ?>