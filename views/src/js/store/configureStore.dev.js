import { createStore, applyMiddleware, compose } from 'redux'
import promise from 'redux-promise-middleware'
import { loadingBarMiddleware } from 'react-redux-loading-bar'
import { rootReducer } from '../reducers'
import thunk from 'redux-thunk'
import { DevTools } from 'Containers'

export default function configureStore(initialState) {
    const store = createStore(
        rootReducer,
        initialState,
        compose(
            applyMiddleware(
                promise(),
                thunk,
                loadingBarMiddleware({
                    promiseTypeSuffixes: ['PENDING', 'FULFILLED', 'REJECTED'],
                })
            ),
            DevTools.instrument()
        )
    )

    if (module.hot) {
        // Enable Webpack hot module replacement for reducers
        module.hot.accept('../reducers', () => {
            const nextRootReducer = require('../reducers').default
            store.replaceReducer(nextRootReducer)
        })
    }

    return store
}
