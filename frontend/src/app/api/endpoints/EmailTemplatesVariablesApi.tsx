// api/endpoints/EmailTemplatesVariablesApi.tsx
import {AxiosRequestConfig} from 'axios';
import {fetchJson} from '@/app/api';
import {URL, URLSearchParams} from "url";



export async function getEmailTemplatesVariables(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/admin/correspondence_services/variables';


    const queryString = Object.keys(searchParams.pagination)
        .filter((key) => searchParams.pagination[key] !== '')
        .map((key) => `${key}=${encodeURIComponent(searchParams.pagination[key])}`)
        .join('&');
    const finalUrl = `${baseUrl}${queryString ? `?${queryString}` : ''}`;

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    };

    return await fetchJson(finalUrl, config);

}
