// api/endpoints/prizeApi.tsx
import { AxiosRequestConfig } from 'axios';
import { fetchJson } from '@/app/api';

export async function getPrizes() {

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    };

    return await fetchJson(`/prizes`, config);
}


export async function getClientBadges(id: string) {
    const token = localStorage.getItem('loggedInUserToken');

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    };

    return await fetchJson(`/client/badges/${id}`, config);
}