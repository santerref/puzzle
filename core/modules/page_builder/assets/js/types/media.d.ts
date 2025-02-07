export type FocalPoint = {
    x: number
    y: number
}

export type StorageFile = {
    id: string
    filename: string
    filemime: string
    filesize: number
    path: string
    is_image: boolean
    width: number | null
    height: number | null
    focal_point_x: number
    focal_point_y: number
    alt: string | null
    title: string | null
}
