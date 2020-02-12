class Core {
    /* Private */
    /* Protected */
    protected static _app: ApplicationInterface = undefined;
    /* Public */

    static app(app: ApplicationInterface = undefined): ApplicationInterface|undefined {
        if (app !== undefined) {
            Core._app = app;
            return Core._app;
        }
        return Core._app;
    }
}
