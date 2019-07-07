import authService from './authService';

const auth = (request) => {
    const user = authService.getUser();
    const token = authService.getToken(user.email, user.password);

    return request({
        'WWW-Authenticate': `${token}`
    });
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
    post,
    get,
    file
};

export default requestService;
