
import { DataGrid } from '@mui/x-data-grid';
import { TextField } from '@mui/material';
import { useEffect, useState } from 'react';
import { fetchUsers, type User } from '../data/mock-users';

export default function UsersTable() {
    const [search, setSearch] = useState("");
    const [rows, setRows] = useState<User[]>([]);

    useEffect(() => {
        fetchUsers().then(users => setRows(users));
    }, []);
    const filteredRows = rows.filter(user =>
        Object.values(user)
            .join(' ')
            .toLowerCase()
            .includes(search.toLowerCase())
    );

    const columns = [
        { field: 'id', headerName: 'ID', width: 70 },
        { field: 'name', headerName: 'Name', flex: 1 },
        { field: 'email', headerName: 'Email', flex: 1 },
        { field: 'role', headerName: 'Role', width: 120 },
        { field: 'joined', headerName: 'Joined Date', width: 150 },
    ];

    return (
        <div style={{ width: '90vw', margin: 'auto' }}>
            <TextField
                label="Search users"
                variant="outlined"
                fullWidth
                margin="normal"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
            />

            <div style={{ height: 400 }}>
                <DataGrid
                    rows={filteredRows}
                    columns={columns}
                    pageSizeOptions={[5, 10]}
                    initialState={{
                        pagination: { paginationModel: { pageSize: 5 } },
                    }}
                />
            </div>
        </div>
    );
}
