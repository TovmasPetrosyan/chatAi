import { Component } from '@angular/core';
import {FormsModule} from '@angular/forms';
import {AiService} from '../ai.service';

@Component({
  selector: 'app-chat',
  imports: [
    FormsModule
  ],
  templateUrl: './chat.html',
  styleUrl: './chat.css',
})
export class Chat {
  msg = '';
  answer = '';
  isLoading = false;
  constructor(private ai: AiService) {}

  send() {
    if (!this.msg.trim() || this.isLoading) return;

    this.isLoading = true;
    this.answer = '';

    this.ai.ask(this.msg).subscribe({
      next: (res: { answer: string }) => {
        this.answer = res.answer;
        this.isLoading = false;
      },
      error: (err) => {
        if (err?.status === 429) this.answer = 'Շատ արագ հարցումներ են գնում․ փորձիր 10-20 վրկ հետո։';
        this.answer = 'Սխալ տեղի ունեցավ 😕';
        this.isLoading = false;
      }
    });
  }

  onEnter(e: Event) {
    const ev = e as KeyboardEvent;

    if (ev.shiftKey) return;

    ev.preventDefault();
    this.send();
  }
}
