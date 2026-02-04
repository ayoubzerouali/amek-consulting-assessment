type ApiUser = {
    id: number;
    name: string;
    email: string;
};
export type User = {
    id: number;
    name: string;
    email: string;
    role: string;
    joined: string;
};

export async function fetchUsers(): Promise<User[]> {

    const response = await fetch('https://jsonplaceholder.typicode.com/users');
    const data = await response.json() as ApiUser[];

    return data.map(user => ({
        id: user.id,
        name: user.name,
        email: user.email,
        role: ['Admin', 'User', 'Manager'][user.id % 3],
        joined: new Date(
            Date.now() - user.id * 86400000 * 30
        ).toISOString().split('T')[0],
    }));
}
