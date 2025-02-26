export {};

declare global {
    interface Window {
        pageUuid: string,
        csrfToken: string
    }
}
