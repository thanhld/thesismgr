import axios from 'axios'

export const setPasswordMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/set-password-email"
    })
}

export const registerTopicMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/register-topic-email"
    })
}

export const changeTopicMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/change-topic-email"
    })
}

export const protectTopicMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/protect-topic-email"
    })
}

export const seminarTopicMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/seminar-topic-email"
    })
}

export const approveTopicMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/approve-topic-email"
    })
}

export const reviewTopicMail = () => {
    axios.request({
        method: 'get',
        baseURL: '/mailer',
        url: "/api/review-topic-email"
    })
}