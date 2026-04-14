<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History To do</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📜 History To do</h3>
        <div>
            <button onclick="deleteAllHistory()"
                    class="btn btn-danger me-2">
                🗑 Delete All
            </button>
            <a href="<?= site_url('todo') ?>"
               class="btn btn-outline-secondary">
                ← Back
            </a>
        </div>
    </div>

    <?php if (empty($todos)): ?>
        <div class="alert alert-info shadow-sm">
            There is no history.
        </div>
    <?php endif; ?>

    <?php foreach ($todos as $todo): ?>

        <?php
            $priority = isset($todo->priority) && $todo->priority
                ? strtolower($todo->priority)
                : 'medium';
        ?>

        <!-- CARD TODO -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">

                <!-- TITLE + DATE -->
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center flex-wrap">

                        <input type="checkbox"
                               class="form-check-input me-2"
                               checked
                               disabled>

                        <strong class="fs-5 text-decoration-line-through">
                            <?= $todo->title ?>
                        </strong>

                        <!-- PRIORITY -->
                        <?php if ($priority === 'high'): ?>
                            <span class="badge bg-danger ms-2">High</span>
                        <?php elseif ($priority === 'medium'): ?>
                            <span class="badge bg-warning text-dark ms-2">Medium</span>
                        <?php elseif ($priority === 'low'): ?>
                            <span class="badge bg-info text-dark ms-2">Low</span>
                        <?php endif; ?>

                        <span class="badge bg-success ms-2">Done</span>
                    </div>

                    <small class="text-muted">
                        <?= date('Y-m-d H:i', strtotime($todo->created_at)) ?>
                    </small>
                </div>

                <!-- SUBTASK LIST -->
                <?php if (!empty($todo->subtasks)): ?>
                    <ul class="list-group list-group-flush mt-3">
                        <?php foreach ($todo->subtasks as $s): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <input type="checkbox"
                                       class="form-check-input me-2"
                                       <?= $s->is_done ? 'checked' : '' ?>
                                       disabled>

                                <span class="<?= $s->is_done ? 'text-decoration-line-through text-muted' : '' ?>">
                                    <?= $s->title ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- ACTION -->
                <div class="mt-3">
                    <button onclick="deleteTodo(<?= $todo->id ?>)"
                            class="btn btn-danger btn-sm">
                        Delete Todo
                    </button>
                </div>

            </div>
        </div>

    <?php endforeach; ?>

</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function deleteTodo(id){
    if(!confirm("Delete this todo from history?")) return;
    $.post("<?= site_url('todo/delete/') ?>" + id, () => location.reload());
}

function deleteAllHistory(){
    if(!confirm("Delete ALL history?")) return;
    $.post("<?= site_url('todo/delete_all_history') ?>", () => location.reload());
}
</script>

</body>
</html>
