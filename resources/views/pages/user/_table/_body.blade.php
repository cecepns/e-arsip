<tbody>
    @forelse($users as $index => $user)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td>{{ $user->username }}</td>
        <td>{{ $user->email }}</td>
        <td>
            <div class="password-container d-flex align-items-center">
                <span class="password-display" id="password-{{ $user->id }}" title="Password: {{ $user->password }}">
                    ••••••••
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 password-toggle" 
                        onclick="togglePassword('{{ $user->id }}')" 
                        title="Toggle password visibility">
                    <i class="fas fa-eye" id="toggle-icon-{{ $user->id }}"></i>
                </button>
            </div>
        </td>
        <td>
            @if($user->bagian)
                {{ $user->bagian->nama_bagian }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center">
            @if($user->role == 'Admin')
                <span class="badge-admin">Admin</span>
            @else
                <span class="badge-staff">Staf</span>
            @endif
        </td>
        <td>
            <div class="action-buttons">
                <button class="action-btn edit-btn" title="Edit" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalUserForm"
                        data-id="{{ $user->id }}"
                        data-username="{{ $user->username }}"
                        data-email="{{ $user->email }}"
                        data-password="{{ $user->password }}"
                        data-role="{{ $user->role }}"
                        data-bagian="{{ $user->bagian_id }}"
                        onclick="showEditUserModal({{ $user->bagian_id }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" title="Hapus" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalDeleteUser"
                        onclick="showDeleteUserModal({{ $user->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center">Tidak ada data user</td>
    </tr>
    @endforelse
</tbody>
