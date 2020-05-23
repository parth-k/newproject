import { TestBed } from '@angular/core/testing';

import { Active2Guard } from './active2.guard';

describe('Active2Guard', () => {
  let guard: Active2Guard;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    guard = TestBed.inject(Active2Guard);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });
});
