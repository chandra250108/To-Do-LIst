<div class="container mt-4">

    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <?= isset($todo) ? '✏️ Edit To do' : '➕ Add To do' ?>
            </h4>
            <form method="post"
                  action="<?= isset($todo)
                      ? site_url('todo/update/'.$todo->id)
                      : site_url('todo/store') ?>">

                <!-- TODO TITLE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">To do Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Enter todo title..."
                           value="<?= isset($todo) ? $todo->title : set_value('title') ?>">
                    <?= form_error('title','<small class="text-danger">','</small>') ?>
                </div>

                <!-- PRIORITY -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">-- Select Priority --</option>
                        <option value="low"
                            <?= isset($todo) && $todo->priority === 'low' ? 'selected' : '' ?>>
                            Low
                        </option>
                        <option value="medium"
                            <?= isset($todo) && $todo->priority === 'medium' ? 'selected' : '' ?>>
                            Medium
                        </option>
                        <option value="high"
                            <?= isset($todo) && $todo->priority === 'high' ? 'selected' : '' ?>>
                            High
                        </option>
                    </select>
                    <?= form_error('priority','<small class="text-danger">','</small>') ?>
                </div>

                <!-- SUBTASK INPUT AREA -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Subtask</label>

                    <div id="subtask_wrapper">
                        <div class="input-group mb-2">
                            <input type="text"
                                   name="subtask[]"
                                   class="form-control"
                                   placeholder="Subtask name...">
                            <button type="button"
                                    class="btn btn-danger remove_subtask">
                                ✕
                            </button>
                        </div>
                    </div>

                    <button type="button"
                            id="add_subtask"
                            class="btn btn-secondary btn-sm mt-2">
                        + Add Subtask
                    </button>
                </div>

                <!-- ACTION -->
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">
                        Save
                    </button>
                    <a href="<?= site_url('todo') ?>"
                       class="btn btn-secondary">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
// tambah subtask input
document.getElementById('add_subtask').addEventListener('click', function() {
    let div = document.createElement('div');
    div.className = "input-group mb-2";
    div.innerHTML = `
        <input type="text" name="subtask[]" class="form-control" placeholder="Subtask name...">
        <button type="button" class="btn btn-danger remove_subtask">✕</button>
    `;
    document.getElementById('subtask_wrapper').appendChild(div);
});

// hapus input subtask
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove_subtask')){
        e.target.parentNode.remove();
    }
});
</script>
