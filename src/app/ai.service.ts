import { Injectable } from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {Observable} from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AiService {
  private apiUrl = 'https://www.avc-agbu.org/edu/api/chat.php';
  constructor(private http: HttpClient) {}

  ask(message: string): Observable<{ answer: string }> {
    return this.http.post<{ answer: string }>(this.apiUrl, { message });
  }
}
