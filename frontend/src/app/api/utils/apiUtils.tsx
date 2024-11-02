import axios, { AxiosRequestConfig } from 'axios';

const BASE_URL = process.env.NEXT_PUBLIC_API_URL;

export async function fetchJson(url: string, options?: AxiosRequestConfig) {
    try {
        if (!options) {
            options = {};
        }
        options.headers = {
            ...options.headers,
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': ['*'],
            'Access-Control-Allow-Headers': ['*'],
            'withCredentials': 'true',
        };

        const response = await axios(`${BASE_URL}${url}`, options);
        return response.data;
    } catch (error:any) {
        throw error;
    }
}
