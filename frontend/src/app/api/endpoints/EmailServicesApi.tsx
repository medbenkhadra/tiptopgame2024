// api/endpoints/EmailTemplatesApi.tsx
import {AxiosRequestConfig} from 'axios';
import {fetchJson} from '@/app/api';
import {URL, URLSearchParams} from "url";


//

export async function getEmailServices() {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/admin/correspondence_services';

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,

        },
    };

    return await fetchJson(baseUrl, config);

}



export async function getEmailTemplatesVariablesByService(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/admin/correspondence_services/services/variables';

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



export async function getEmailingHistory(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    console.log("searchParamssearchParamssearchParams", searchParams);

    const baseUrl = '/emailing_history';

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
        }
    };

    return await fetchJson(finalUrl, config);
}