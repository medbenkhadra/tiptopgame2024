// api/endpoints/badges
import { AxiosRequestConfig } from 'axios';
import { fetchJson } from '@/app/api';

export async function getAllBadges() {
    const token = localStorage.getItem('loggedInUserToken');

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    };

    return await fetchJson(`/badges`, config);
}
