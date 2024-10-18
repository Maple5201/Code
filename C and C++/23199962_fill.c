#include <GL/glut.h>
#include <math.h>
#include <stdio.h>
#include <stdlib.h>

#define WINDOW_WIDTH 500
#define WINDOW_HEIGHT 550

typedef struct {
    float x, y;
    float r, g, b;
} Vertex;


float M = WINDOW_HEIGHT / 3.0; 


Vertex vertices[4] = {
    {WINDOW_WIDTH / 2, WINDOW_HEIGHT / 2 + M, 1.0, 0.0, 0.0},  
    {WINDOW_WIDTH / 2 - M, WINDOW_HEIGHT / 2, 0.0, 1.0, 0.0},  
    {WINDOW_WIDTH / 2, WINDOW_HEIGHT / 2 - 2 * M, 0.0, 0.0, 1.0},  
    {WINDOW_WIDTH / 2 + 2 * M, WINDOW_HEIGHT / 2, 0.0, 0.0, 0.0}   
};

void drawLine(Vertex v1, Vertex v2) {
    float dx = v2.x - v1.x;
    float dy = v2.y - v1.y;
    int steps = (int)(fabs(dx) > fabs(dy) ? fabs(dx) : fabs(dy));
    float xInc = dx / (float)steps;
    float yInc = dy / (float)steps;
    float rInc = (v2.r - v1.r) / (float)steps;
    float gInc = (v2.g - v1.g) / (float)steps;
    float bInc = (v2.b - v1.b) / (float)steps;

    float x = v1.x, y = v1.y, r = v1.r, g = v1.g, b = v1.b;
    for (int i = 0; i <= steps; i++) {
        glColor3f(r, g, b);
        glVertex2f(x, y);
        x += xInc;
        y += yInc;
        r += rInc;
        g += gInc;
        b += bInc;
    }
}

void fillPolygon() {
    
    float minY = vertices[0].y, maxY = vertices[0].y;
    for (int i = 1; i < 4; i++) {
        if (vertices[i].y < minY) minY = vertices[i].y;
        if (vertices[i].y > maxY) maxY = vertices[i].y;
    }
    for (float scanY = minY; scanY <= maxY; scanY += 0.01) {
        Vertex intersections[4];
        int count = 0;
        for (int i = 0; i < 4; i++) {
            Vertex v1 = vertices[i];
            Vertex v2 = vertices[(i + 1) % 4];
            if ((v1.y <= scanY && v2.y >= scanY) || (v1.y >= scanY && v2.y <= scanY)) {
                float t = (scanY - v1.y) / (v2.y - v1.y);
                intersections[count++] = (Vertex){
                    v1.x + t * (v2.x - v1.x),
                    scanY,
                    v1.r + t * (v2.r - v1.r),
                    v1.g + t * (v2.g - v1.g),
                    v1.b + t * (v2.b - v1.b)
                };
            }
        }
        if (count == 2) {
            if (intersections[0].x > intersections[1].x) {
                Vertex temp = intersections[0];
                intersections[0] = intersections[1];
                intersections[1] = temp;
            }
            glBegin(GL_POINTS);
            drawLine(intersections[0], intersections[1]);
            glEnd();
        }
    }
}

void display() {
    glClear(GL_COLOR_BUFFER_BIT);
    glBegin(GL_POINTS);
    fillPolygon();
    glEnd();
    glFlush();
}

void init() {
    glClearColor(0.0, 0.0, 0.0, 1.0);
    gluOrtho2D(0, WINDOW_WIDTH, 0, WINDOW_HEIGHT);
}

int main(int argc, char** argv) {
    glutInit(&argc, argv);
    glutInitDisplayMode(GLUT_SINGLE | GLUT_RGB);
    glutInitWindowSize(WINDOW_WIDTH, WINDOW_HEIGHT);
    glutCreateWindow("Polygon Fill with Scanline Interpolation");
    init();
    glutDisplayFunc(display);
    glutMainLoop();
    return 0;
}
