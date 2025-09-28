<tbody>
    @forelse($users as $index => $user)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td>{{ $user->username }}</td>
        <td>{{ $user->nama ?? '-' }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->phone ?? '-' }}</td>
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
        <td class="text-center">
            @if($user->isKepalaBagian())
                <span class="badge bg-warning text-dark">Kepala Bagian</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <div class="action-buttons">
                <button class="action-btn edit-btn" title="Edit" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalEditUser"
                        onclick="showEditUserModal({{ $user->id }})">
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
        <td colspan="9" class="text-center">Tidak ada data user</td>
    </tr>
    @endforelse
</tbody>
