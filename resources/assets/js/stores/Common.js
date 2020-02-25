//import CommonCRUD from "./CommonCRUD";

export default {
    state: {
        tabIndex: 0,
        requestSuccess: false,
        dimension:{
            width: null,
            height: null,
        },
        auth: {
            role: null,
            token: null,
            name: null
        },
        yandexMapSettings: {
            apiKey: 'a4ce5e3d-3d09-41be-b223-f2eb24d5439b', // '' by default
            lang: 'ru_RU', // 'ru_RU' by default
            version: '2.1' // '2.1' by default
        },
        managerPanel: {}, // reserved
        webLocator: {}, // reserved

    },
};