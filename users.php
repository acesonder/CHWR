<?php
require_once __DIR__ . '/includes/auth.php';
requireRole([ROLE_ADMIN, ROLE_MANAGEMENT]);

$pageTitle = 'User Management';
include __DIR__ . '/includes/header.php';

$conn = getDBConnection();

// Get all users
$usersQuery = "
    SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.is_active, u.created_at, r.id as role_id, r.role_name 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    ORDER BY u.created_at DESC
";
$usersResult = $conn->query($usersQuery);

// Get all roles for the dropdown
$rolesResult = $conn->query("SELECT id, role_name FROM roles ORDER BY role_name");
$roles = [];
while ($role = $rolesResult->fetch_assoc()) {
    $roles[] = $role;
}

$conn->close();
?>

<div class="dashboard-header">
    <h2>User Management</h2>
    <p>Manage user accounts and permissions</p>
</div>

<?php if (hasRole(ROLE_ADMIN)): ?>
<div style="margin-bottom: 1.5rem;">
    <button onclick="openModal('addUserModal')" class="btn btn-success">
        + Add New User
    </button>
</div>
<?php endif; ?>

<div class="content-card">
    <div style="margin-bottom: 1rem;">
        <input type="text" id="searchInput" class="form-control" placeholder="Search users..." style="max-width: 300px;">
    </div>
    
    <div class="table-container">
        <table id="usersTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usersResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php 
                        $badgeClass = 'badge-' . strtolower(str_replace(' ', '-', $user['role_name']));
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($user['role_name']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $user['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if (hasRole(ROLE_ADMIN)): ?>
                            <button onclick="editUser(<?php echo $user['id']; ?>)" class="btn btn-primary btn-sm">
                                Edit
                            </button>
                            <?php if ($user['username'] !== 'admin'): ?>
                            <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" class="btn btn-danger btn-sm">
                                Delete
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New User</h3>
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
        </div>
        <form id="addUserForm">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="role_id">Role *</label>
                <select id="role_id" name="role_id" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked> Active
                </label>
            </div>
            
            <button type="submit" class="btn btn-success">Create User</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit User</h3>
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        </div>
        <form id="editUserForm">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_user_id" name="id">
            
            <div class="form-group">
                <label for="edit_username">Username *</label>
                <input type="text" id="edit_username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit_email">Email *</label>
                <input type="email" id="edit_email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit_password">Password (leave blank to keep current)</label>
                <input type="password" id="edit_password" name="password" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_first_name">First Name *</label>
                <input type="text" id="edit_first_name" name="first_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit_last_name">Last Name *</label>
                <input type="text" id="edit_last_name" name="last_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit_role_id">Role *</label>
                <select id="edit_role_id" name="role_id" class="form-control" required>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1"> Active
                </label>
            </div>
            
            <button type="submit" class="btn btn-success">Update User</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
