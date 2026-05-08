<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php';
$movies = get_all_movies($connection);
$categories = get_all_categories($connection);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Movie Management</h4>
    <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addMovieModal">
        <i class="fas fa-plus me-2"></i> Add Movie
    </button>
</div>

<!-- Category Management -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-muted"><i class="fas fa-tags me-2"></i>Categories</h6>
        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#categoryManage">
            <i class="fas fa-cog me-1"></i> Manage
        </button>
    </div>
    <div class="collapse" id="categoryManage">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <form action="../code.php" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="admin_action" value="add_category_inline">
                        <input type="text" name="category_name" class="form-control form-control-sm" placeholder="New category name" required>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i></button>
                    </form>
                </div>
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($categories as $cat): ?>
                        <span class="badge bg-light text-dark border px-2 py-2">
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                            <form action="../code.php" method="POST" class="d-inline ms-1">
                                <input type="hidden" name="admin_action" value="delete_category_inline">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="btn btn-link btn-sm p-0 text-danger" onclick="return confirm('Delete this category?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm border rounded-pill px-3">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="movieSearch" class="form-control bg-transparent border-0 shadow-none" placeholder="Search movies...">
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="movieTable">
            <thead>
                <tr>
                    <th class="ps-4">MOVIE</th>
                    <th>CATEGORIES</th>
                    <th>RELEASE</th>
                    <th>RATING</th>
                    <th>STATUS</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="../images/<?php echo $movie['poster'] ?: 'default.jpg'; ?>" class="rounded-3 shadow-sm" style="width:40px; height:55px; object-fit:cover;">
                            <div>
                                <div class="fw-bold text-main"><?php echo htmlspecialchars($movie['title']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($movie['duration']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="small text-muted"><?php echo htmlspecialchars($movie['categories'] ?? 'N/A'); ?></span></td>
                    <td><span class="small"><?php echo date('M d, Y', strtotime($movie['release_date'])); ?></span></td>
                    <td>
                        <div class="d-flex align-items-center gap-1 text-warning small">
                            <i class="fas fa-star"></i>
                            <span class="text-main fw-bold"><?php echo number_format((float)($movie['rating'] ?? 0), 1); ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo ($movie['movie_status'] == 'now_showing') ? 'success' : 'info'; ?>-subtle text-<?php echo ($movie['movie_status'] == 'now_showing') ? 'success' : 'info'; ?> border px-2 py-1">
                            <?php echo ucfirst(str_replace('_', ' ', $movie['movie_status'])); ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light border edit-movie-btn" 
                                data-id="<?php echo $movie['id']; ?>"
                                data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                                data-duration="<?php echo htmlspecialchars($movie['duration']); ?>"
                                data-release="<?php echo htmlspecialchars($movie['release_date']); ?>"
                                data-trailer="<?php echo htmlspecialchars($movie['trailer_link']); ?>"
                                data-desc="<?php echo htmlspecialchars($movie['movie_desc']); ?>"
                                data-director="<?php echo htmlspecialchars($movie['director']); ?>"
                                data-language="<?php echo htmlspecialchars($movie['language']); ?>"
                                data-status="<?php echo $movie['movie_status']; ?>"
                            ><i class="fas fa-edit text-primary"></i></button>
                            <form action="../code.php" method="POST" class="d-inline">
                                <input type="hidden" name="admin_action" value="delete_movie">
                                <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-light border" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addMovieModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Add New Movie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="admin_action" value="add_movie">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">MOVIE TITLE</label>
                            <input type="text" name="title" class="form-control shadow-none" placeholder="Inception" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">STATUS</label>
                            <select name="movie_status" class="form-select shadow-none">
                                <option value="now_showing">Now Showing</option>
                                <option value="upcoming">Upcoming</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">CATEGORIES</label>
                            <div class="border rounded p-2" style="max-height: 100px; overflow-y: auto;">
                                <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $cat['id']; ?>" id="cat_<?php echo $cat['id']; ?>">
                                    <label class="form-check-label small" for="cat_<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">DURATION</label>
                            <input type="text" name="duration" class="form-control shadow-none" placeholder="2h 28m" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">DIRECTOR</label>
                            <input type="text" name="director" class="form-control shadow-none" placeholder="Christopher Nolan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">LANGUAGE</label>
                            <input type="text" name="language" class="form-control shadow-none" placeholder="English">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">RELEASE DATE</label>
                            <input type="date" name="release_date" class="form-control shadow-none">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">POSTER IMAGE</label>
                            <input type="file" name="poster" class="form-control shadow-none" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">TRAILER URL</label>
                            <input type="text" name="trailer_link" class="form-control shadow-none" placeholder="Enter YouTube link">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">DESCRIPTION</label>
                            <textarea name="movie_desc" class="form-control shadow-none" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4 shadow-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-none fw-bold">Save Movie</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editMovieModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Edit Movie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="admin_action" value="update_movie">
                <input type="hidden" name="id" id="editMovieId">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">MOVIE TITLE</label>
                            <input type="text" name="title" id="editMovieTitle" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">STATUS</label>
                            <select name="movie_status" id="editMovieStatus" class="form-select shadow-none">
                                <option value="now_showing">Now Showing</option>
                                <option value="upcoming">Upcoming</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">CATEGORIES</label>
                            <div class="border rounded p-2" style="max-height: 100px; overflow-y: auto;">
                                <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $cat['id']; ?>" id="edit_cat_<?php echo $cat['id']; ?>">
                                    <label class="form-check-label small" for="edit_cat_<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">DURATION</label>
                            <input type="text" name="duration" id="editMovieDuration" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">DIRECTOR</label>
                            <input type="text" name="director" id="editMovieDirector" class="form-control shadow-none">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">LANGUAGE</label>
                            <input type="text" name="language" id="editMovieLanguage" class="form-control shadow-none">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">RELEASE DATE</label>
                            <input type="date" name="release_date" id="editMovieRelease" class="form-control shadow-none">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">POSTER (OPTIONAL)</label>
                            <input type="file" name="poster" class="form-control shadow-none" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">TRAILER LINK</label>
                            <input type="text" name="trailer_link" id="editMovieTrailer" class="form-control shadow-none">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">DESCRIPTION</label>
                            <textarea name="movie_desc" id="editMovieDesc" class="form-control shadow-none" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4 shadow-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-none fw-bold">Update Movie</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const movieSearch = document.getElementById('movieSearch');
    if (movieSearch) {
        movieSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#movieTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }

    const editMovieButtons = document.querySelectorAll('.edit-movie-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editMovieModal'));
    
    editMovieButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editMovieId').value = this.dataset.id;
            document.getElementById('editMovieTitle').value = this.dataset.title;
            document.getElementById('editMovieDuration').value = this.dataset.duration;
            document.getElementById('editMovieRelease').value = this.dataset.release;
            document.getElementById('editMovieTrailer').value = this.dataset.trailer;
            document.getElementById('editMovieDesc').value = this.dataset.desc;
            document.getElementById('editMovieDirector').value = this.dataset.director;
            document.getElementById('editMovieLanguage').value = this.dataset.language;
            document.getElementById('editMovieStatus').value = this.dataset.status;
            editModal.show();
        });
    });
});
</script>

<style>
.bg-success-subtle { background-color: #dcfce7 !important; }
.bg-info-subtle { background-color: #e0f2fe !important; }
.text-success { color: #166534 !important; }
.text-info { color: #0369a1 !important; }
</style>

<?php include_once 'admin_footer.php'; ?>