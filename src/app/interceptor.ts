import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';


@Injectable()
export class Interceptor implements HttpInterceptor {
    constructor() { }

    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        let email = localStorage.getItem('user_email')

        if (email) {
            request = request.clone({
                setHeaders: {
                     'Authorization': `${email}`
                }
            });
        }

        return next.handle(request);
    }
}
