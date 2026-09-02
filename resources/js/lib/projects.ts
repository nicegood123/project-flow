export interface Project {
    id: number;
    client_name: string;
    project_name: string;
    description: string | null;
    status: 'Planning' | 'In Progress' | 'On Hold' | 'Completed';
    priority: 'Low' | 'Medium' | 'High';
    start_date: string | null;
    due_date: string | null;
}

export interface ProjectPayload {
    client_name: string;
    project_name: string;
    description: string | null;
    status: Project['status'];
    priority: Project['priority'];
    start_date: string | null;
    due_date: string | null;
}

export interface ProjectFilters {
    search?: string;
    status?: Project['status'] | '';
    priority?: Project['priority'] | '';
    sort?:
        | 'project_name'
        | 'client_name'
        | 'start_date'
        | 'due_date'
        | 'priority'
        | 'status'
        | '';
    direction?: 'asc' | 'desc';
}

interface ProjectResponse {
    data: Project;
}

interface ProjectsResponse {
    data: Project[];
}

export class ApiError extends Error {
    constructor(
        message: string,
        public readonly errors: Record<string, string[]> = {},
        public readonly status?: number,
    ) {
        super(message);
    }
}

function csrfToken(): string | undefined {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
        ?.content;
}

async function request<T>(url: string, options?: RequestInit): Promise<T> {
    const headers = new Headers(options?.headers);
    headers.set('Accept', 'application/json');

    const token = csrfToken();
    if (token) {
        headers.set('X-CSRF-TOKEN', token);
    }

    const response = await fetch(url, {
        ...options,
        headers,
    });

    if (!response.ok) {
        const body = (await response.json().catch(() => null)) as {
            errors?: Record<string, string[]>;
            message?: string;
        } | null;

        throw new ApiError(
            body?.message ??
                'Unable to complete the request. Please try again.',
            body?.errors,
            response.status,
        );
    }

    return response.status === 204 ? (undefined as T) : response.json();
}

export async function getProjects(
    filters: ProjectFilters = {},
): Promise<Project[]> {
    const parameters = new URLSearchParams();

    if (filters.search?.trim()) {
        parameters.set('search', filters.search.trim());
    }

    if (filters.status) {
        parameters.set('status', filters.status);
    }

    if (filters.priority) {
        parameters.set('priority', filters.priority);
    }

    if (filters.sort) {
        parameters.set('sort', filters.sort);
        parameters.set('direction', filters.direction ?? 'asc');
    }

    const query = parameters.size > 0 ? `?${parameters.toString()}` : '';
    const response = await request<ProjectsResponse>(`/api/projects${query}`);

    return response.data;
}

export async function getProject(id: number): Promise<Project> {
    const response = await request<ProjectResponse>(`/api/projects/${id}`);

    return response.data;
}

export async function createProject(payload: ProjectPayload): Promise<Project> {
    const response = await request<ProjectResponse>('/api/projects', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
    });

    return response.data;
}

export async function updateProject(
    id: number,
    payload: ProjectPayload,
): Promise<Project> {
    const response = await request<ProjectResponse>(`/api/projects/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
    });

    return response.data;
}

export async function deleteProject(id: number): Promise<void> {
    await request<ProjectResponse>(`/api/projects/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    });
}
