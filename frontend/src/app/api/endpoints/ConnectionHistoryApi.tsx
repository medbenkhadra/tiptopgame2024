// api/endpoints/ConnectionHistoryApi
import { AxiosRequestConfig } from 'axios';
import { fetchJson } from '@/app/api';


export async function getConnectionHistory(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/connection_history';

    const queryString = Object.keys(searchParams)
        .filter((key) => searchParams[key] !== '')
        .map((key) => `${key}=${encodeURIComponent(searchParams[key])}`)
        .join('&');
    const finalUrl = `${baseUrl}${queryString ? `?${queryString}` : ''}`;
    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,

        },
    };

    return await fetchJson(finalUrl, config);

}