const requestService = {
    post: (url, data) =>  {
        return axios.post(url, data, {
            headers: {
                'Content-type': 'application/json'
            }
        });
    },
    file: (url, file) => {
        const fd = new FormData();

        fd.append('file', file);

        return axios.post(url, fd, {
            headers: {
                'Content-type': 'multipart/form-data'
            }
        });
    }
};

export default requestService;
