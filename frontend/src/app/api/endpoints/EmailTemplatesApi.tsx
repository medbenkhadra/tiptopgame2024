// api/endpoints/EmailTemplatesApi.tsx
import {AxiosRequestConfig} from 'axios';
import {fetchJson} from '@/app/api';
import {URL, URLSearchParams} from "url";


//

export async function getCorrespandanceTemplates(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/admin/correspondence_templates';

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

export async function createNewTemplate(data: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/admin/correspondence_template/create';

    data = JSON.stringify(data);

    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,

        },
        data: data
    };

    return await fetchJson(baseUrl, config);
}


export async function getEmailTemplateById(id: string) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = `/admin/correspondence_template/${id}`;

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,

        },
    };

    return await fetchJson(baseUrl, config);
}

//update template
export async function updateEmailTemplate(id : any , data: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/admin/correspondence_template/'+id+'/update';

    data = JSON.stringify(data);

    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,

        },
        data: data
    };

    return await fetchJson(baseUrl, config);
}