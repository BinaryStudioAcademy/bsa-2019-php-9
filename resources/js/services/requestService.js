import authService from './authService';

const getAuthHeader = (user) => {
    const token = authService.getToken(user.email, user.password);

    return {
        'WWW-Authenticate': `${token}`
    };
};

const auth = (request) => {
    const user = authService.getUser();

    return request(getAuthHeader(user));
};

const post = (url, data, headers = {}) => auth((authHeader) => axios.post(url, data, {
    headers: {
        'Content-type': 'application/json',
        ...headers,
        ...authHeader
    }
}));

const get = (url, params) => auth((headers) => axios.get(url, { params }, { headers }));

const file = (url, file) => {
    const fd = new FormData();

    fd.append('photo', file);

    return post(url, fd, {
        'Content-type': 'multipart/form-data',        
    });
}

const requestService = {
    getAuthHeader,
    post,
    get,
    file
};

export default requestService;
