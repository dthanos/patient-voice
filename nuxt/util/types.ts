export interface Patient {
    id: string;
    name: string;
    email: string;
    gender: string;
    disease: string;
    medication: string;
    age: number;
}

export interface DatatableApi {
    index: string;
    create: string;
    update: string;
    delete: string;
}
