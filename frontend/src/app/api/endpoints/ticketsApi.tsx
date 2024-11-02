import {AxiosRequestConfig} from 'axios';
import {fetchJson} from '@/app/api';
import {URL, URLSearchParams} from "url";

interface SearchParams {
    page?: string;
    limit?: string;
    center?: string;
    user?: string;
    status?: string;
    caissier?: string;
    client?: string;
    sort?: string;
    order?: string;
}

export async function getTickets(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/tickets';



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



export async function getTicketsPending(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/tickets/pending';

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



export async function getTicketByCode(searchParam: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const url = '/ticket/'+searchParam;

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    };

    return await fetchJson(url, config);
}





//checkTicketCodeValidity
export async function checkTicketCodeValidity(ticketCode: string) {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        data: JSON.stringify({
            ticketCode: ticketCode,
        }),
    };

    return await fetchJson(`/tickets/check/play`, config);
}

export async function confirmPrintTicket(data: any) {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        data: {
            ticketCode: data,
        },
    };

    return await fetchJson(`/print_ticket`, config);
}


export async function printRandomTicket() {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };

    return await fetchJson(`/print_random_ticket`, config);
}


export async function confirmTicketPlayed(ticketCode: string) {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        data: {
            ticketCode: ticketCode,
        },
    };

    return await fetchJson(`/ticket/confirm/play`, config);
}

//confimGainTicket
export async function confimGainTicket(ticketId: string) {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        data: {
            ticketId: ticketId,
        },
    };

    return await fetchJson(`/ticket/confirm/gain`, config);
}

//getGainTicket
export async function getGainTicket(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/winner_tickets';

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


export async function getGainTicketHistory(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/winner_tickets/history';

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


export async function getTicketsHistory(searchParams: any) {
    const token = localStorage.getItem('loggedInUserToken');

    const baseUrl = '/tickets_history';

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


export async function testFinalDrawCall() {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };

    return await fetchJson(`/final_draw_test`, config);
}

export async function realFinalDrawCall() {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };

    return await fetchJson(`/final_draw`, config);
}


export async function getFinalDrawHistory() {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };

    return await fetchJson(`/final_draw/history`, config);
}



export async function getGameConfig() {

    const url = '/game_config';

    const config: AxiosRequestConfig = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    };


    return await fetchJson(url, config);
}



export async function updateGameConfig(data: any) {
    const token = localStorage.getItem('loggedInUserToken');
    const config: AxiosRequestConfig = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        data: JSON.stringify(data),
    };

    return await fetchJson(`/game_config/update`, config);
}