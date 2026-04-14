<div class="container mt-4">

    <?php
        // HITUNG TODO AKTIF (is_done = 0)
        $activeTodos = array_filter($todos, function($t){
            return !$t->is_done;
        });
    ?>

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📝 To do List</h3>
        <div>
            <a href="<?= site_url('todo/history') ?>" class="btn btn-outline-secondary me-2">
                📜 History
            </a>
            <a href="<?= site_url('todo/create') ?>" class="btn btn-primary">
                + Add To do
            </a>
        </div>
    </div>

    <?php if (empty($activeTodos)): ?>
        <div class="alert alert-info text-center shadow-sm">
        There is no Todo's
        </div>
    <?php endif; ?>

    <?php foreach ($todos as $todo): ?>

        <?php
            // DONE tidak tampil di halaman utama
            if ($todo->is_done) continue;

            // PRIORITY fallback aman
            $priority = isset($todo->priority) && $todo->priority
                ? strtolower($todo->priority)
                : 'medium';
        ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">

                <!-- TITLE + DATE -->
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <input type="checkbox"
                               class="form-check-input me-2 todo-checkbox"
                               data-todo-id="<?= $todo->id ?>"
                               onclick="toggleTodo(<?= $todo->id ?>)">

                        <strong class="fs-5">
                            <?= $todo->title ?>
                        </strong>

                        <!-- PRIORITY BADGE -->
                        <?php if ($priority === 'high'): ?>
                            <span class="badge bg-danger ms-2">High</span>
                        <?php elseif ($priority === 'medium'): ?>
                            <span class="badge bg-warning text-dark ms-2">Medium</span>
                        <?php elseif ($priority === 'low'): ?>
                            <span class="badge bg-info text-dark ms-2">Low</span>
                        <?php endif; ?>

                        <span class="badge bg-secondary ms-2">Ongoing</span>
                    </div>

                    <small class="text-muted"><?= $todo->created_at ?></small>
                </div>

                <!-- DELETE TODO -->
                <button onclick="deleteTodo(<?= $todo->id ?>)"
                        class="btn btn-danger btn-sm mt-3">
                    Delete To do
                </button>

                <!-- SUBTASK LIST -->
                <ul class="list-group list-group-flush mt-3"
                    id="subtask_list_<?= $todo->id ?>">
                    <?php foreach ($todo->subtasks as $s): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <input type="checkbox"
                                   class="form-check-input me-2 subtask-checkbox"
                                   data-subtask-id="<?= $s->id ?>"
                                   data-todo-id="<?= $todo->id ?>"
                                   onclick="toggleSubtask(<?= $s->id ?>, <?= $todo->id ?>)"
                                   <?= $s->is_done ? 'checked' : '' ?>>
                            <?= $s->is_done ? '<s>'.$s->title.'</s>' : $s->title ?>
                        </span>

                        <div>
                            <button class="btn btn-warning btn-sm"
                                onclick="editSubtask(<?= $s->id ?>, '<?= htmlspecialchars($s->title, ENT_QUOTES) ?>')">
                                Edit
                            </button>
                            <button class="btn btn-danger btn-sm"
                                onclick="deleteSubtask(<?= $s->id ?>)">
                                Delete
                            </button>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <!-- ADD SUBTASK -->
                <div class="input-group mt-3">
                    <input type="text"
                           id="new_subtask_<?= $todo->id ?>"
                           class="form-control"
                           placeholder="Add subtask...">
                    <button class="btn btn-secondary"
                            onclick="addSubtask(<?= $todo->id ?>)">
                        Add
                    </button>

            </div>
        </div>

    <?php endforeach; ?>

</div>

<!-- jQuery (WAJIB) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// ================= TOGGLE TODO =================
function toggleTodo(todoId){
    let checked = $(`.todo-checkbox[data-todo-id="${todoId}"]`).prop("checked");

    $.post("<?= site_url('todo/toggle/') ?>" + todoId, function(){
        $(`#subtask_list_${todoId} .subtask-checkbox`).each(function(){
            if($(this).prop("checked") !== checked){
                $(this).prop("checked", checked);
                toggleSubtaskOnly($(this).data("subtask-id"));
            }
        });
        location.reload();
    });
}

// ================= TOGGLE SUBTASK =================
function toggleSubtask(subtaskId, todoId){
    $.post("<?= site_url('todo/ajax_toggle_subtask') ?>", {id: subtaskId}, function(){
        let allChecked = true;
        $(`#subtask_list_${todoId} .subtask-checkbox`).each(function(){
            if(!$(this).prop("checked")) allChecked = false;
        });

        if(allChecked){
            toggleTodoOnly(todoId);
        }
        location.reload();
    });
}

function toggleSubtaskOnly(id){
    $.post("<?= site_url('todo/ajax_toggle_subtask') ?>", {id});
}

function toggleTodoOnly(id){
    $.post("<?= site_url('todo/toggle/') ?>" + id);
}

// ================= CRUD =================
function deleteTodo(id){
    if(!confirm("Delete this todo?")) return;
    $.post("<?= site_url('todo/delete/') ?>" + id, () => location.reload());
}

function addSubtask(todo_id){
    let title = $("#new_subtask_" + todo_id).val().trim();
    if(!title) return;

    $.post("<?= site_url('todo/ajax_add_subtask') ?>", {
        todo_id, title
    }, () => location.reload());
}

function deleteSubtask(id){
    if(!confirm("Delete this subtask?")) return;
    $.post("<?= site_url('todo/ajax_delete_subtask') ?>", {id}, () => location.reload());
}

function editSubtask(id, oldTitle){
    let title = prompt("Edit subtask:", oldTitle);
    if(!title) return;

    $.post("<?= site_url('todo/ajax_edit_subtask') ?>", {
        id, title
    }, () => location.reload());
}
</script>
