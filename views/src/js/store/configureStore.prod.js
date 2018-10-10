import { createStore, applyMiddleware, compose } from 'redux'
import promise from 'redux-promise-middleware'
import { loadingBarMiddleware } from 'react-redux-loading-bar'
import { rootReducer } from '../reducers'
import thunk from 'redux-thunk'

// Middleware you want to use in production:
const enhancer = applyMiddleware(
    promise(),
    thunk,
    loadingBarMiddleware({
        promiseTypeSuffixes: ['PENDING', 'FULFILLED', 'REJECTED'],
    })
);

export default function configureStore(initialState) {
  // Note: only Redux >= 3.1.0 supports passing enhancer as third argument.
  // See https://github.com/rackt/redux/releases/tag/v3.1.0
  return createStore(rootReducer, initialState, enhancer)
}
