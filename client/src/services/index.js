import api from './api'

export const authService = {
  register: (data) => api.post('/auth/register', data),
  login: (data) => api.post('/auth/login', data),
  getCurrentUser: () => api.get('/auth/me')
}

export const userService = {
  getAllUsers: () => api.get('/users'),
  getUserProfile: (userId) => api.get(`/users/${userId}`),
  updateProfile: (userId, data) => api.put(`/users/${userId}`, data),
  deleteUser: (userId) => api.delete(`/users/${userId}`)
}

export const photoService = {
  getAllPhotos: (params) => api.get('/photos', { params }),
  getPhotoById: (fotoId) => api.get(`/photos/${fotoId}`),
  uploadPhoto: (formData) => api.post('/photos', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  updatePhoto: (fotoId, data) => api.put(`/photos/${fotoId}`, data),
  deletePhoto: (fotoId) => api.delete(`/photos/${fotoId}`),
  getUserPhotos: (userId) => api.get(`/photos/user/${userId}`)
}

export const albumService = {
  getAllAlbums: () => api.get('/albums'),
  getAlbumById: (albumId) => api.get(`/albums/${albumId}`),
  createAlbum: (data) => api.post('/albums', data),
  updateAlbum: (albumId, data) => api.put(`/albums/${albumId}`, data),
  deleteAlbum: (albumId) => api.delete(`/albums/${albumId}`),
  getUserAlbums: (userId) => api.get(`/albums/user/${userId}`)
}

export const likeService = {
  toggleLike: (fotoId) => api.post(`/likes/${fotoId}`),
  getLikeStatus: (fotoId) => api.get(`/likes/${fotoId}/status`),
  getPhotoLikes: (fotoId) => api.get(`/likes/${fotoId}`)
}

export const commentService = {
  addComment: (fotoId, data) => api.post(`/comments/${fotoId}`, data),
  getPhotoComments: (fotoId, params) => api.get(`/comments/${fotoId}`, { params }),
  deleteComment: (komentarId) => api.delete(`/comments/${komentarId}`)
}
